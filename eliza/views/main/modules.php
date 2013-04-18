<div class="modules-page">
  <h1>Extend Eliza with additional modules</h1>
  <p>Eliza allows you extend its functionality with additional modules. On this page you can download modules and
  read documentation to each of them.</p>
  <div class="left-column span3">
    <ul class="menu">
      <?php foreach($modules as $showModule): ?>
        <li><a href="<?php echo $this->url('main/modules',array('module'=>$showModule['id'])) ?>"><?php echo $showModule['name'] ?></a></li>

          <?php if ($module && ($module['id'] == $showModule['id'])): ?>
          <ul class="sub">
            <?php foreach($methods as $method): ?>
              <li><a href="#<?php echo $method['short_name'] ?>"><?php echo $method['short_name'] ?>()</a></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="middle-column span8">

    <?php if ($module): ?>
      <div class="module-name"><?php echo $module['name'] ?></div>
      <div class="description"><?php echo $module['description'] ?></div>
    <?php endif; ?>
    <div class="title-methods">Module methods</div>
    <?php foreach ($methods as $method): ?>
      <div class="method-description" id="<?php echo $method['short_name'] ?>">
        <div class="title"><?php echo htmlspecialchars($method['name']); ?></div>
        <hr />
        <span class="label">Params:</span>
        <?php echo $method['params']; ?>
        <span class="label">Description:</span>
        <p><?php echo htmlspecialchars($method['description']); ?></p>
        <span class="label">Example:</span>
        <?php $geshi = new GeSHi('','php') ?>
        <?php $geshi->set_source($method['example']) ?>
        <?php echo $geshi->parse_code() ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>