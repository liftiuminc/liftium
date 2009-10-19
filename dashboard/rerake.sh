#!/bin/bash
if [ "$1" == "" ] ; then
  env="development"
else
  env=$1
fi

if [ "$1" == "live" ] ; then
  echo "No more reraking the live website. You owe Nick a lunch for putting this check here, you would have just dropped the live database."
fi

rake db:drop RAILS_ENV=$env
rake db:create RAILS_ENV=$env
rake db:migrate RAILS_ENV=$env

### loading fixtures does not appear to work if they are not in the spec/ dir
### tried with FIXTURES_DIR to an absolute path, relative path and ., no luck
### despite what the usage says. strace'd the processes to be sure. Switched to
### a symlink to get it running again. -Jos
# $ rake -D spec:db:fixtures:load
#     Load fixtures (from spec/fixtures) into the current environment's database.  Load specific fixtures using FIXTURES=x,y. Load from subdirectory in test/fixtures using FIXTURES_DIR=z.
#rake spec:db:fixtures:load FIXTURES_DIR=. RAILS_ENV=$env
rake spec:db:fixtures:load RAILS_ENV=$env
