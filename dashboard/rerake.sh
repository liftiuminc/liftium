#!/bin/bash
if [ "$1" == "" ] ; then
  env="development"
else
  env=$1
fi

if [ "$1" == "live" ] ; then
  echo "No more reraking the live website. You owe Nick a margarita for putting this check here, you would have just dropped the live database."
fi

rake db:drop RAILS_ENV=$env
rake db:create RAILS_ENV=$env
rake db:migrate RAILS_ENV=$env
rake db:fixtures:load RAILS_ENV=$env
