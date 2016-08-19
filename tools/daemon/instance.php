<?

$lock='daemon';//unique name of instance soft

   if( file_exists( sys_get_temp_dir()."/".$lock.".pid" ))
   {
       $pid = file_get_contents( sys_get_temp_dir()."/".$lock.".pid" );
       if( file_exists( "/proc/$pid" ))
       {
           error_log( "сервис уже запущен.");
           exit(1);
       }
       else
       {
           error_log( "предыдущий процесс завершен не корректно, исправление" );
           unlink( sys_get_temp_dir()."/".$lock.".pid" );
       }
   }
   $h = fopen( sys_get_temp_dir()."/".$lock.".pid" , "w");
   if( $h ) fwrite( $h, getmypid() );
   fclose( $h );

declare(ticks = 1);

// signal handler function
function sig_handler($signo)
{

     switch ($signo) {
         case SIGTERM:
             // handle shutdown tasks
             exit;
             break;
         case SIGHUP:
             // handle restart tasks
             break;
         case SIGUSR1:
             echo "Caught SIGUSR1...\n";
             break;
         default:
             // handle all other signals
     }

}

echo "Installing signal handler...\n";

// setup signal handlers
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");

// or use an object, available as of PHP 4.3.0
// pcntl_signal(SIGUSR1, array($obj, "do_something");

echo"Generating signal SIGTERM to self...\n";

// send SIGUSR1 to current process id
posix_kill(posix_getpid(), SIGUSR1);

echo "Done\n";


sleep(360);
?>
