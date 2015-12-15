
CURRENT_DIRECTORY=`dirname "$0"`

php -d open_basedir= "${CURRENT_DIRECTORY}/build-hosts-list.php"
