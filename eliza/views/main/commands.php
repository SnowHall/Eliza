<?php $geshi = new GeSHi('', 'php'); ?>
<div class="commands" style="overflow: hidden;">
  <h3>Test commands</h3>
  <div class="left-column" style="width: 150px; float: left; padding-top: 10px;">
    <ul>
      <li><a href="#click">Click</a></li>
      <li><a href="#go">Go</a></li>
      <li><a href="#want">Want</a></li>
    </ul>
  </div>
  <div class="right-column" style="width: 700px; float: left;">
    <div class="command-description" id="click">
      <h4>Click($link)</h4>
      <hr />
      <p><strong>Params:</strong><br /> </p>
      <ul>
        <li>$link - clicked link text.</li>
      </ul>
      <p><strong>Description:</strong><br /> </p>
      <p>Allows to emulate user's click on link or submit form. Results of this action - response of POST or GET request to specify page.</p>
      <p><strong>Example:</strong></p>
      <?php $geshi->set_source('$I->click(\'Login\');') ?>
      <p><?php echo $geshi->parse_code() ?></p>
    </div>
    <div class="command-description" id="go">
      <h4>Go($link)</h4>
      <hr />
      <p><strong>Params:</strong><br /> </p>
      <ul>
        <li>$link - clicked link text.</li>
      </ul>
      <p><strong>Description:</strong><br /> </p>
      <p>Allows to emulate user's opening specify page. Results of this action - response of GET request to specify page. Param "/" according to the site main page.</p>
      <p><strong>Example:</strong></p>
      <?php $geshi->set_source('$I->go(\'/\');') ?>
      <p><?php echo $geshi->parse_code() ?></p>
    </div>
    <div class="command-description" id="want">
      <h4>Want($description)</h4>
      <hr />
      <p><strong>Params:</strong><br /> </p>
      <ul>
        <li>$description - text of test description.</li>
      </ul>
      <p><strong>Description:</strong><br /> </p>
      <p>Allows to add comments before test execution. It helps describe what test do.</p>
      <p><strong>Example:</strong></p>
      <?php $geshi->set_source('$I->want(\'Check Authorization\');') ?>
      <p><?php echo $geshi->parse_code() ?></p>
    </div>
  </div>
</div>