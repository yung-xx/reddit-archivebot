<div class="content-dis">
<p><form class="options" method="post">
<span class="param">SUBREDDIT</span><br>
<p>r/&nbsp;&nbsp;<input type="text" value="<?php if (isset($_POST['subreddit'])) echo $_POST['subreddit'];?>" placeholder="GenderCritical" name="subreddit" class="str-input" required /></p>
<p>Max. connections&nbsp;&nbsp;<input type="number" min='1' max='1000' value="<?php if (isset($_POST['rate'])) echo $_POST['rate']; else echo '200';?>" placeholder="200" name="rate" class="str-input" style="width: 60px;" required />
Timeout&nbsp;&nbsp;<input type="number" min='1' max='60' value="<?php if (isset($_POST['timeout'])) echo $_POST['timeout']; else echo '30';?>" placeholder="30" name="timeout" class="str-input" style="width: 60px;" required />(sec.)</p>
<p>From&nbsp;&nbsp;<input type='datetime-local' name='after' value="<?php if (!empty($_POST['after'])) echo date("Y-m-d\TH:i", $_POST['after']);?>" class="str-input" style="margin-right:0px;" required>&nbsp;&nbsp;&nbsp;to&nbsp;&nbsp;
<input type='datetime-local' name='before' value="<?php if (!empty($_POST['before'])) echo date("Y-m-d\TH:i", $_POST['before']);?>" class="str-input" style="margin-right:0px;"></p>
<br>
<span class="param">ADVANCED</span><br>
<p>u/&nbsp;&nbsp;<input type="text" value="<?php if (isset($_POST['author'])) echo $_POST['author'];?>" placeholder="username" name="author" class="str-input" /></p>
<p>Keywords&nbsp;&nbsp;<input type="text" value="<?php if (isset($_POST['keywords'])) echo $_POST['keywords'];?>" placeholder="keyword" name="keywords" class="str-input" style="width: 150px;" /></p>
<p>Score&nbsp;&nbsp;<input type="text" value="<?php if (isset($_POST['score'])) echo $_POST['score'];?>" name="score" placeholder=">10,<20" class="str-input" style="width: 80px;" /></p>
<br>
<span class="param">ACTIONS</span><br><br>
<input type="radio" name="backup" id="backup1" value="no" <?php if (!isset($_POST['backup']) OR $_POST['backup']=='no') echo 'checked'; else ''?> />
<label for="backup1">List-only</label>
<input type="radio" name="backup" id="backup2" value="yes" <?php if (isset($_POST['backup']) AND $_POST['backup']=='yes') echo 'checked';?> />
<label for="backup2">Backup</label>
<br><br>
<span class="param">OPTIONAL</span><br><br>
<input type="checkbox" name="smart" id="cb1" <?php if (isset($_POST['smart'])) echo 'checked';?> />
<label for="cb1">Smart</label>
<input type="checkbox" name="simple" id="cb4" <?php if (isset($_POST['simple'])) echo 'checked';?> />
<label for="cb4">Simple</label>
<input type="checkbox" name="compression" id="cb2" <?php if (isset($_POST['compression'])) echo 'checked';?> />
<label for="cb2">Compression</label>
<input type="checkbox" name="debug" id="cb3" <?php if (isset($_POST['debug'])) echo 'checked';?> />
<label for="cb3">Debug</label>
<br><br>
<span class='input-button' style='cursor:pointer;'>SUBMIT<input type='submit' name='submit' value='' style='position: absolute;height: 100%;width: 100%;left: -2px;background: transparent;cursor: pointer;border: 0px;'></span>
</form></p>
</div>