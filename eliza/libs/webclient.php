<?php

/**
 * Eliza - Simple php acceptance testing framework
 *
 *
 * @author		SnowHall - http://snowhall.com
 * @website		http://elizatesting.com
 * @email		support@snowhall.com
 *
 * @version		0.2.0
 * @date		April 18, 2013
 *
 * Eliza - simple framework for BDD development and acceptance testing.
 * Eliza has user-friendly web interface that allows run and manage your tests from your favorite browser.
 *
 * Copyright (c) 2009-2013
 */

class Webclient
{
  public $first_checking = false;
  public $_redirect_url = '';
  private $_user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10';
  private $_datamethod = 'socket';
	private $_usegzip = false;
	private $tmpfolder = '/tmp';
  public $_real_url = '';
	private $_debugdir = '.log';
	private $_debugnum = 1;
	private $_delay = 1;
	private $_body = array();
  public $_cookies = array();
	public $_my_cookies = array();
	private $_addressbar = '';
	private $_multipart = false;
	private $_timestart = 0;
	private $_bytes = 0;
  private $_errno = 0;
  private $_errstr = '';
  private $_timeout = 10;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		/* check if zlib is available */
		if (function_exists('gzopen')) {
			//$this->_usegzip = true;
		}

		/* start time */
		$this->_timestart = microtime(true);
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		/* remove temporary file for gzip encoding */
		if (isset($this->tmpfname) && file_exists($this->tmpfname)) {
			unlink($this->tmpfname);
		}

		/* get elapsed time and transferred bytes */
		$time  = sprintf("%02.1f", microtime(true) - $this->_timestart);
		$bytes = sprintf("%d", ceil($this->_bytes / 1024));

	}

	/**
	 * SET GZIP
	 */
	public function set_zip($gzip_status) {
      $this->_usegzip = $gzip_status;
	}

	/**
	 * SET REFERER
	 */
	public function set_referer($url) {
      $this->_referer = $url;
	}

	public function clear_cookie() {
      $this->_cookies = array();
	}

	public function use_data_method($data) {
      $this->_datamethod = $data;
	}

	/**
	 * HEAD
	 */
	public function head($url = '')
	{
	    if($url == '') return $this->head;
		return $this->fetch($url, 'HEAD');
	}

	/**
	 * GET
	 */
	public function get($url, $maxredir = 10)
	{
		return $this->fetch($url, 'GET', $maxredir);
	}

	/**
	 * POST
	 */
	public function post($url, $form = array(), $files = array())
	{
		return $this->fetch($url, 'POST', 10, $form, $files);
	}

	/**
	 * Make HTTP request
	 */
	protected function fetch($url, $method, $maxredir = 10, $form = array(), $files = array(), $pre_load=false, $redir = false)
	{

		/* convert to absolute if relative URL */
		$url = $this->getAbsUrl($url, $this->_addressbar);

		/* only http or https */
		if(substr($url, 0, 4) != 'http') return '';

		/* cache URL */
		$this->_addressbar = $url;

		/* build request */
		$reqbody = $this->getReqBody($form, $files);
		$reqhead = $this->getReqHead($url, $method, strlen($reqbody), empty($files) ? false : true);

		/* parse URL and convert to local variables:
		   $scheme, $host, $path */
		$parts = parse_url($url);
		$port_real = '';
		if(isset($parts['port'])) $port_real = $parts['port'];
		if (!$parts) {
			die("Invalid URL!\r\n");
		} else {
			foreach($parts as $key=>$val) $$key = $val;
		}

		/* open connection */
    $port1 = ($scheme == 'https' ? 443 : 80);
    if($port_real != '') $port1 = $port_real;
    if(isset($this->_port) && $this->_port > 0) $port1 = $this->_port;

    $fp = @fsockopen(($scheme=='https' ? "ssl://$host" : $host), $port1, $this->_errno, $this->_errstr, 10);

		if (!$fp) {
			return array('error' => 'host_connect');//die("Cannot connect to $host!\r\n");
		}

    @fwrite($fp, $reqhead.$reqbody);
    @stream_set_timeout($fp, $this->_timeout);
    $res = '';
    $i = 0;
    while($c = @fread($fp, 5421772))
    {
        $res .= $c;
        $i++;
        if($i>1000 || $c == '') break;
    }
    fclose($fp);

		sleep($this->_delay);

		$this->_bytes += (strlen($reqhead)+ strlen($reqbody)+ strlen($res));

		list($reshead, $resbody) = explode("\r\n\r\n", $res, 2);

		$this->head = $this->parseHead($reshead);

		if ($method == 'HEAD') {
			return $this->head;
		}

		if (!empty($this->head['Set-Cookie'])) {
			$this->saveCookies($this->head['Set-Cookie'], $url);
		}

		if ($this->head['Status']['Code'] == 200) {
			$this->_referer = $url;
		}

		if (isset($this->head['Transfer-Encoding']) && ($this->head['Transfer-Encoding'] == 'chunked')) {
			$body = $this->joinChunks($resbody);
		} else {
			$body = $resbody;
		}

		if (isset($this->head['Content-Encoding']) && ($this->head['Content-Encoding'] == 'gzip')) {
      $body = $this->unzip_html($body);
		}

    array_unshift($this->_body, $body);

		if ((isset($this->head['Location']) || isset($this->head['location'])) && $maxredir > 0) {
      $location = isset($this->head['Location']) ? $this->head['Location'] : $this->head['location'];
      $redirectto = $this->getAbsUrl($location, $url);

		  $this->_redirect_url = $redirectto;
      $this->_real_url = $redirectto;

		  return $this->fetch($redirectto, 'GET', $maxredir-1, null, null, null, (str_replace('http://', 'http://www.', $url) == $redirectto? false : true));
		}

		$meta = $this->parseMetaTags($body);

		if (isset($meta['http-equiv']['refresh']) && $maxredir > 0) {
			list($delay, $loc) = explode(';', $meta['http-equiv']['refresh'], 2);
			$loc = substr(trim($loc), 4);
			if (!empty($loc) && $loc != $url) {
        $redirectto = $this->getAbsUrl($loc, $url);

				return $this->fetch($redirectto, 'GET', $maxredir--);
			}
		}

		for($i = 1; $i < count($this->_body); $i++) {
			unset($this->_body[$i]);
		}
    $this->cookie_url = $url;

		return $body;
	}

    function unzip_html($body)
    {
        $this->tmpfname = tempnam($this->tmpfolder, "page");
        @file_put_contents($this->tmpfname, $body);
        $body = '';
        $fp = @gzopen($this->tmpfname, 'r');
        if($fp === FALSE) return array('error' => 'proxy_connect');
        $i = 0;
        while (!@gzeof($fp)) {
          $body .= @gzgets($fp, 4096);
          $i++;
          if($body == '') { break; }
        }
        if($i==1000) return array('error' => 'proxy_connect');
        @gzclose($fp);
        if (file_exists($this->tmpfname)) unlink($this->tmpfname);
        return $body;
    }

    function setCookies($cookies)
    {
      $this->_my_cookies = $cookies;
    }

	/**
	 * Build request header
	 */
	protected function getReqHead($url, $method, $bodylen = 0, $sendfile = true)
	{
		/* parse URL elements to local variables:
		   $scheme, $host, $path, $query, $user, $pass */
		$parts = parse_url($url);
		foreach($parts as $key=>$val) $$key = $val;

		/* setup path */
		$path = empty($path)  ? '/' : $path
			  .(empty($query) ? ''  : "?$query");

		/* request header */
  	$head = "$method $path HTTP/1.1\r\nHost: $host\r\n";

		/* cookies */
		$head .= $this->getCookies($url);

		/* content-type */
		if ($method == 'POST' && ($sendfile || $this->_multipart)) {
			$head .= "Content-Type: multipart/form-data;\r\n";
		} elseif ($method == 'POST') {
			$head .= "Content-Type: application/x-www-form-urlencoded\r\n";
		}

		/* set the content length if POST */
		if ($method == 'POST') {
			$head .= "Content-Length: $bodylen\r\n";
		}

		/* basic authentication */
		if (!empty($user) && !empty($pass)) {
			$head .= "Authorization: Basic ". base64_encode("$user:$pass")."\r\n";
		}


		/* gzip */
		if ($this->_usegzip) {
			//$head .= "Accept-Encoding: deflate\r\n";
			$head .= "Accept-Encoding: gzip\r\n";
		}
    $head .= "Accept-Language: en-us,en\r\n";
		/* make it like real browsers */
		if (!empty($this->_user_agent)) {
			$head .= "User-Agent: $this->_user_agent\r\n";
		}
        if (!empty($this->_referer)) {
            $head .= "Referer: $this->_referer\r\n";
        }
		if (is_array($this->_my_cookies) && !empty($this->_my_cookies)) {
			$head .= "Cookie: ";
            foreach($this->_my_cookies as $name=>$value)
                $head .= "$name=$value; ";
            $head .= "\r\n";
		}

		/* no pipelining yet */
		$head .= "Connection: Close\r\n\r\n";

		/* request header is ready */
		return $head;
	}

	/**
	 * Build request body
	 */
	protected function getReqBody($form = array(), $files = array())
	{
		/* check for parameters */
		if (empty($form) && empty($files))
			return '';

		$body = '';
		$tmp  = array();

		/* only form available: x-www-urlencoded */
		if (!empty($form) &&  empty($files) && !$this->_multipart) {
			foreach($form['fields'] as $key=>$val)
				$tmp[] = $key .'='. urlencode($val);
			return implode('&', $tmp);
		}

		/* form */
		foreach($form as $key=>$val) {
			$body .= "Content-Disposition: form-data; name=\"" . $key ."\"\r\n\r\n" . $val ."\r\n";
		}

		/* files */
		foreach($files as $key=>$val) {
			if (!file_exists($val)) continue;
			$body .= "Content-Disposition: form-data; name=\"" . $key . "\"; filename=\"" . basename($val) . "\"\r\n"
          . "Content-Type: " . $this->getMimeType($val) . "\r\n\r\n"
          . file_get_contents($val) . "\r\n";
		}

		/* request body is ready! */
		return $body;
	}

	/**
	 * convert response header to associative array
	 */
	protected function parseHead($str)
	{
		$lines = explode("\r\n", $str);

		list($ver, $code, $msg) = explode(' ', array_shift($lines), 3);
		$stat = array('Version' => $ver, 'Code' => $code, 'Message' => $msg);

		$head = array('Status' => $stat);

		foreach($lines as $line) {
			list($key, $val) = explode(':', $line, 2);
			if ($key == 'Set-Cookie') {
				$head['Set-Cookie'][] = trim($val);
			} else {
				$head[$key] = trim($val);
			}
		}

		return $head;
	}

	/**
	 * Read chunked pages
	 */
	protected function joinChunks($str)
	{
		$CRLF = "\r\n";
		for($tmp = $str, $res = ''; !empty($tmp); $tmp = trim($tmp)) {
			if (($pos = strpos($tmp, $CRLF)) === false) return $str;
			$len = hexdec(substr($tmp, 0, $pos));
			$res.= substr($tmp, $pos + strlen($CRLF), $len);
			$tmp = substr($tmp, $pos + strlen($CRLF) + $len);
		}
		return $res;
	}

	/**
	 * Save cookies from server
	 */
	public function saveCookies($set_cookies, $url)
	{
		foreach($set_cookies as $str)
		{
			$parts = explode(';', $str);

			/* extract cookie parts to local variables:
			   $name, $value, $domain, $path, $expires, $secure, $httponly */
			foreach($parts as $part) {
				list($key, $val) = explode('=', trim($part), 2);

				$k = strtolower($key);

				if ($k == 'secure' || $k == 'httponly') {
					$$k = true;
				} elseif ($k == 'domain' || $k == 'path' || $k == 'expires') {
					$$k = $val;
				} else {
					$name  = $key;
					$value = $val;
				}
			}

			/* cookie's domain */
			if (empty($domain)) {
				$domain = parse_url($url, PHP_URL_HOST);
			}

			/* cookie's path */
			if (empty($path)) {
				$path = parse_url($url, PHP_URL_PATH);
				$path = preg_replace('#/[^/]*$#', '', $path);
				$path = empty($path) ? '/' : $path;
			}

			/* cookie's expire time */
			if (!empty($expires)) {
				$expires = strtotime($expires);
			}

			/* setup cookie ID, a simple trick to add/update existing cookie
			   and cleanup local variables later */
			$id = md5("$domain;$path;$name");

			if(!isset($secure)) $secure = '';
			if(!isset($httponly)) $httponly = '';
			if(!isset($expires)) $expires = '';

			/* add/update cookie */
			$this->_cookies[$id] = array(
				'domain'   => substr_count($domain, '.') == 1 ? ".$domain" : $domain,
				'path'     => $path,
				'expires'  => $expires,
				'name'     => $name,
				'value'    => $value,
				'secure'   => $secure,
				'httponly' => $httponly
			);

			/* cleanup local variables */
			foreach($this->_cookies[$id] as $key=>$val) unset($$key);
		}

		return true;
	}

	/**
	 * Get cookies for URL
	 */
	protected function getCookies($url)
	{
		$tmp = array();
		$res = array();

		/* remove expired cookies first */
		foreach($this->_cookies as $id=>$cookie) {
			if (empty($cookie['expires']) || $cookie['expires'] >= time()) {
				$tmp[$id] = $cookie;
			}
		}

		/* cookies ready */
		$this->_cookies = $tmp;

		/* parse URL to local variables:
		   $scheme, $host, $path, $query */
		$parts = parse_url($url);
		foreach($parts as $key=>$val) $$key = $val;

		if (empty($path)) $path = '/';

		/* get all cookies for this domain and path */
		foreach($this->_cookies as $cookie) {
			$d = substr($host, -1 * strlen($cookie['domain']));
			$p = substr($path, 0, strlen($cookie['path']));

			if (($d == $cookie['domain'] || ".$d" == $cookie['domain']) && $p == $cookie['path']) {
				if ($cookie['secure'] == true  && $scheme == 'http') {
					continue;
				}
				$res[] = $cookie['name'].'='.$cookie['value'];
			}
		}

		/* return the string for HTTP header */
		return (empty($res) ? '' : 'Cookie: '.implode('; ', $res)."\r\n");
	}

	/**
	 * Convert relative URL to absolute URL
	 */
	protected function getAbsUrl($loc, $parent)
	{
		/* parameters is required */
		if (empty($loc) && empty($parent)) return;

		$loc = str_replace('&amp;', '&', $loc);

		/* return if URL is abolute */
		if (parse_url($loc, PHP_URL_SCHEME) != '') return $loc;

		/* handle anchors and query's part */
		$c = substr($loc, 0, 1);
		if ($c == '#' || $c == '&') return "$parent$loc";

		/* handle query string */
		if ($c == '?') {
			$pos = strpos($parent, '?');
			if ($pos !== false) $parent = substr($parent, 0, $pos);
			return "$parent$loc";
		}

		/* parse URL and convert to local variables:
		   $scheme, $host, $path */
		$parts = parse_url($parent);
		foreach ($parts as $key=>$val) $$key = $val;

		/* remove non-directory part from path */
		$path = preg_replace('#/[^/]*$#', '', $path);

		/* set path to '/' if empty */
		$path = preg_match('#^/#', $loc) ? '/' : $path;

		/* dirty absolute URL */
		$abs = "$host$path/$loc";

		/* replace '//', '/./', '/foo/../' with '/' */
		while($abs = preg_replace(array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'), '/', $abs, -1, $count))
			if (!$count) break;

		/* absolute URL */
		return "$scheme://$abs";
	}

	/**
	 * Convert meta tags to associative array
	 */
	protected function parseMetaTags($html)
	{
		/* extract to </head> */
		if (($pos = strpos(strtolower($html), '</head>')) === false) {
			return array();
		} else {
			$head = substr($html, 0, $pos);
		}

		/* get page's title */
		preg_match("/<title>(.+)<\/title>/siU", $head, $m);
		$meta = array('title' => $m[1]);

		/* get all <meta...> */
		preg_match_all('/<meta\s+[^>]*name\s*=\s*[\'"][^>]+>/siU', $head, $m);
		foreach($m[0] as $row) {
			preg_match('/name\s*=\s*[\'"](.+)[\'"]/siU', $row, $key);
			preg_match('/content\s*=\s *[\'"](.+)[\'"]/siU', $row, $val);
			if (!empty($key[1]) && !empty($val[1]))
				$meta[$key[1]] = $val[1];
		}

		/* get <meta http-equiv=refresh...> */
		preg_match('/<meta[^>]+http-equiv\s*=\s*[\'"]?refresh[\'"]?[^>]+content\s*=\s*[\'"](.+)[\'"][^>]*>/siU', $head, $m);
		if (!empty($m[1])) {
			$meta['http-equiv']['refresh'] = preg_replace('/&#0?39;/', '', $m[1]);
		}
		return $meta;
	}

	public function parseAllForms($action = '', $str = '')
	{
		if (empty($str) && empty($this->_body[0]))
			return array();

		$body = empty($str) ? $this->_body[0] : $str;

    /* TODO Change on regular expression (remove scripts that contains form tags) */
    $body = str_replace('"</form>', '', $body);

		/* extract the form */
		if (!preg_match_all("/<form([^>]*)>.+<\/form>/si", $body, $form)) {
			return array();
    }

    preg_match_all('/<block\W|([a-z_]+=[\'"]{1}[^\'^"]+[\'"]{1})|>/s',
    '<block wef="e2f" norv="12asd345" abc="werty">', $matches);

		$ret = array();
		if(is_array($form[0]))
		foreach ($form[0] as $k=>$v)
		{
      /* Find Id and name form */
      if (isset($form[1][$k])) {
        preg_match('/name="([a-zA-Z0-9_]+)"/', $form[1][$k], $formName);
        $temp['name'] = isset($formName[1]) ? $formName[1] : '';
        preg_match('/id="([a-zA-Z0-9_]+)"/', $form[1][$k], $formId);
        $temp['id'] = isset($formId[1]) ? $formId[1] : '';
      }

			preg_match_all("/action=['\"]+([^'\"]*)['\"]+/siU", $form[1][$k], $action);
			$temp['action'] = $action[1][0];
			preg_match_all("/method=['\"]+([^'\"]*)['\"]+/siU", $form[1][$k], $method);
			$temp['method'] = $method[1][0];
			$res = array();

      preg_match_all('/<select[^>]+name\s*=\s*(?(?=[\'\"])[\'\"]([^>]+)[\'\"]|\b([^>]+)\b)[^>]*>(.*)<\/select>/siU', $v, $a);

      $select = array();
      if (!empty($a[1])) {
        foreach($a[1] as $num=>$key) {
          preg_match_all('/<option[^>]+value\s*=\s*(?(?=[\'\"])[\'\"]([^>]+)[\'\"]|\b([^>]+)\b)[^>]*>(.*)<\/option>/siU',$a[3][$num],$optionsParts);
          $options = array();
          foreach($optionsParts[1] as $optNum=>$optKey) {
            $options[$optKey] = $optionsParts[3][$optNum];
          }
          if ($key == '') $key = $a[2][$num];
          $select[$key] = $options;
        }
      }

			/* get all <input...> */
			preg_match('/<textarea[^>]+name="([^"]*)"/siU', $v, $a);
      if (isset($a[1])) $res[$a[1]] = '';

			/* get all <input...> */
			preg_match_all('/<input([^>]+)\/?>/siU', $v, $a);

			/* convert to associative array */
			foreach($a[1] as $b) {
				preg_match_all('/([a-z]+)\s*=\s*(?(?=[\'"])[\'"]([^"]+)[\'"]|\b(.+)\b)/siU', trim($b), $c);

				$element = array();

				foreach($c[1] as $num=>$key) {
					$val = $c[2][$num];
					if ($val == '') $val = $c[3][$num];
					$element[$key] = $val;
				}

				$type = strtolower($element['type']);

				/* only radio or checkbox with default values */
				if ($type == 'radio' || $type == 'checkbox')
					if (!preg_match('/\s+\bchecked\b/', $b)) continue;

        if ($type == 'submit')
        {
          $temp['submit'] = $element['value'];
        }

				/* remove buttons and file */
				if ($type == 'file' /*|| $type == 'submit' || $type == 'reset' || $type == 'button'*/)
					continue;

				/* remove unnamed elements */
				if (isset($element['name'], $element['id']) && $element['name'] == '' && $element['id'] == '')
					continue;

				/* cool */
				$key = !empty($element['name']) ? $element['name'] : $element['id'];
				$res[$key] = isset($element['value']) ? html_entity_decode($element['value']) : '';
			}

      //Set form's action
      if (!$temp['action']) $temp['action'] = $this->_addressbar;

      $temp['fields'] = $res;
      $temp['select'] = $select;
			$ret[] = $temp;
		}
		return $ret;
	}


    public function parseAllFormsFull($action = '', $str = '')
    {
        if (empty($str) && empty($this->_body[0]))
            return array();

        $body = empty($str) ? $this->_body[0] : $str;

        /* extract the form */
        if (!preg_match_all("/<form([^>]*)>.+<\/form>/siU", $body, $form))
            return array();

        $ret = array();
        if(is_array($form[0]))
        foreach ($form[0] as $k=>$v)
        {
            preg_match_all("/action=['\"]+([^'\"]*)['\"]+/siU", $form[1][$k], $action);
            $temp['action'] = $action[1][0];
            preg_match_all("/method=['\"]+([^'\"]*)['\"]+/siU", $form[1][$k], $method);
            $temp['method'] = $method[1][0];
            $res = array();
            /* select all <select..> with default values */
            $re = '<select[^>]+name\s*=\s*(?(?=[\'"])[\'"]([^>]+)[\'"]|\b([^>]+)\b)[^>]*>'
                . '.+value\s*=\s*(?(?=[\'"])[\'"]([^>]+)[\'"]|\b([^>]+)\b)[^>]+\bselected\b'
                . '.+<\/select>';
            preg_match_all("/$re/siU", $v, $a);

            foreach($a[1] as $num=>$key) {
                $val = $a[3][$num];
                if ($val == '') $val = $a[4][$num];
                if ($key == '') $key = $a[2][$num];
                $res[$key] = html_entity_decode($val);
            }

            /* get all <input...> */
            preg_match('/<textarea[^>]+name="([^"]*)"/siU', $v, $a);
            $res[$a[1]] = '';


            /* get all <input...> */
            preg_match_all('/<input([^>]+)\/?>/siU', $v, $a);

            /* convert to associative array */
            foreach($a[1] as $b) {
                preg_match_all('/([a-z]+)\s*=\s*(?(?=[\'"])[\'"]([^"]+)[\'"]|\b(.+)\b)/siU', trim($b), $c);

                $element = array();

                foreach($c[1] as $num=>$key) {
                    $val = $c[2][$num];
                    if ($val == '') $val = $c[3][$num];
                    $element[$key] = $val;
                }

                $type = strtolower($element['type']);

                /* only radio or checkbox with default values */
                if ($type == 'radio' || $type == 'checkbox')
                    if (!preg_match('/\s+\bchecked\b/', $b)) continue;

                /* remove buttons and file */
                if ($type == 'file' || $type == 'submit' || $type == 'reset' || $type == 'button')
                    continue;

                /* remove unnamed elements */
                if ($element['name'] == '' && $element['id'] == '')
                    continue;

                /* cool */
                $key = $element['name'] == '' ? $element['id'] : $element['name'];
                $res[$key] = array('type'=>$type, 'value'=>html_entity_decode($element['value']));
            }
            $temp['fields'] = $res;
            $ret[] = $temp;
        }
        return $ret;
    }

	/**
	 * Convert form to associative array
	 */
	public function parseForm($name_or_id, $action = '', $str = '')
	{
		if (empty($str) && empty($this->_body[0]))
			return array();

		$body = empty($str) ? $this->_body[0] : $str;

		/* extract the form */
		$re = '(<form[^>]+(id|name)\s*=\s*(?(?=[\'"])[\'"]'.$name_or_id.'[\'"]|\b'.$name_or_id.'\b)[^>]*>.+<\/form>)';
		if (!preg_match("/$re/siU", $body, $form))
			return array();

		/* check if enctype=multipart/form-data */
		if (preg_match('/<form[^>]+enctype[^>]+multipart\/form-data[^>]*>/siU', $form[1], $a))
			$this->_multipart = true;
		else
			$this->_multipart = false;

		/* get form's action */
		preg_match('/<form[^>]+action\s*=\s*(?(?=[\'"])[\'"]([^\'"]+)[\'"]|([^>\s]+))[^>]*>/si', $form[1], $a);
		$action = empty($a[1]) ? html_entity_decode($a[2]) : html_entity_decode($a[1]);

		/* select all <select..> with default values */
		$re = '<select[^>]+name\s*=\s*(?(?=[\'"])[\'"]([^>]+)[\'"]|\b([^>]+)\b)[^>]*>'
			. '.+value\s*=\s*(?(?=[\'"])[\'"]([^>]+)[\'"]|\b([^>]+)\b)[^>]+\bselected\b'
			. '.+<\/select>';
		preg_match_all("/$re/siU", $form[1], $a);

		foreach($a[1] as $num=>$key) {
			$val = $a[3][$num];
			if ($val == '') $val = $a[4][$num];
			if ($key == '') $key = $a[2][$num];
			$res[$key] = html_entity_decode($val);
		}

		/* get all <input...> */
		preg_match('/<textarea[^>]+name="([^"]*)"/siU', $form[1], $a);
		$res[$a[1]] = '';


		/* get all <input...> */
		preg_match_all('/<input([^>]+)\/?>/siU', $form[1], $a);

		/* convert to associative array */
		foreach($a[1] as $b) {
			preg_match_all('/([a-z]+)\s*=\s*(?(?=[\'"])[\'"]([^"]+)[\'"]|\b(.+)\b)/siU', trim($b), $c);

			$element = array();

			foreach($c[1] as $num=>$key) {
				$val = $c[2][$num];
				if ($val == '') $val = $c[3][$num];
				$element[$key] = $val;
			}

			$type = strtolower($element['type']);

			/* only radio or checkbox with default values */
			if ($type == 'radio' || $type == 'checkbox')
				if (!preg_match('/\s+\bchecked\b/', $b)) continue;

			/* remove buttons and file */
			if ($type == 'file' || $type == 'submit' || $type == 'reset' || $type == 'button')
				continue;

			/* remove unnamed elements */
			if ($element['name'] == '' && $element['id'] == '')
				continue;

			/* cool */
			$key = $element['name'] == '' ? $element['id'] : $element['name'];
			$res[$key] = html_entity_decode($element['value']);
		}

		return $res;
	}

	/**
	 * Get mime type for a file
	 */
	protected function getMimeType($filename)
	{
		/* list of mime type. add more rows to suit your need */
		$mimetypes = array(
			'jpg'  => 'image/jpeg',
			'jpe'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'gif'  => 'image/gif',
			'png'  => 'image/png',
			'tiff' => 'image/tiff',
			'html' => 'text/html',
			'txt'  => 'text/plain',
			'pdf'  => 'application/pdf',
			'zip'  => 'application/zip'
		);

		/* get file extension */
		preg_match('#\.([^\.]+)$#', strtolower($filename), $e);

		/* get mime type */
		foreach($mimetypes as $ext=>$mime)
			if ($e[1] == $ext) return $mime;

		/* this is the default mime type */
		return 'application/octet-stream';
	}


	/**
	 * Set delay between requests
	 */
	public function setInterval($sec)
	{
		if (!preg_match('/^\d+$/', $sec) || $sec <= 0) {
			$this->_delay = 1;
		} else {
			$this->_delay = $sec;
		}
	}

	/**
	 * Assign a name for this HTTP client
	 */
	public function setUserAgent($ua)
	{
		$this->_user_agent = $ua;
	}
}