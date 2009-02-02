<?php
// http://sourceforge.net/projects/meritomony

//the path to the php includes
$pathincludes = 'includes/';

//database config file
require 'private/dbconfig.php';

require 'includes/startup.php';

require 'includes/input.php';


//load db based modules
require 'includes/opendb.php';


require 'includes/configure.php';

require 'includes/access.php';

require 'includes/admin.php';




//see what part to load

if ($inputView == "atp")
{
//load automatic teller panel
require 'includes/atp.php';
}


elseif ($inputView == "cron")
{
require 'includes/cron.php';

}

elseif ($inputView == "blank")
{
echo "blanc";
}

elseif ($inputView == "wrapper")
{
require 'includes/wrapper.php';
}

else {

$inputView = "home";

require 'includes/home.php';
}




require 'includes/closedb.php';

require 'includes/footer.php';


require 'includes/status.php';

require 'includes/render.php';


?>