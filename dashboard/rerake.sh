#!/bin/bash
if [ "$1" == "" ] ; then
  env="development"
else
  env=$1
fi

rake db:drop RAILS_ENV=$env
rake db:create RAILS_ENV=$env
rake db:migrate RAILS_ENV=$env
rake db:fixtures:load RAILS_ENV=$env
