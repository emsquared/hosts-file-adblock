
CURRENT_DIRECTORY=`dirname "$0"`

php -d open_basedir= -d display_errors=1 "${CURRENT_DIRECTORY}/build-hosts-list.php"
