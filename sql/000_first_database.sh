#!/bin/sh

echo "CREATE DATABASE IF NOT EXISTS \`development_db\` ;" | "${mysql[@]}"
echo "GRANT ALL ON \`second_database\`.* TO '"$MYSQL_USER"'@'%' ;" | "${mysql[@]}"
echo 'FLUSH PRIVILEGES ;' | "${mysql[@]}"