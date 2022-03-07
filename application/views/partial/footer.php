</div>
</div>
<div id="footer">
    <?php echo $this->lang->line('common_powered_by').' Open Source Point Of Sale'; ?>; Copyright &copy; 2011 by <a href="http://ospos.pappastech.com" target="_blank">Pappas Technologies, LLC</a><br />Modified for Marlene's Soap Shop by <a href="">EJ Balilo</a>. Some rights reserved.
    <br />
    <?php
        $api_tunnel = json_decode(file_get_contents("http://localhost:4040/api/tunnels"));
        $public_url = $api_tunnel->tunnels[0]->public_url .'/isasys';
    ?>
    Public URL: <a href='<?php echo $public_url; ?>' target='_blank'><?php echo $public_url; ?></a>
</div>
</body>
</html>