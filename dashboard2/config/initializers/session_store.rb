# Be sure to restart your server when you modify this file.

# Your secret key for verifying cookie session data integrity.
# If you change this key, all old sessions will become invalid!
# Make sure the secret is at least 30 characters and all random, 
# no regular words or you'll be exposed to dictionary attacks.
ActionController::Base.session = {
  :key         => '_dashboard2_session',
  :secret      => '914f65dff05c53d4c50dee5b3fe32640c36686859f34d01cabc1c0c895456d999a88fc528c2321518ae6df396340668532f52d0f1f2f2f5f1c669f982a6736cb'
}

# Use the database for sessions instead of the cookie-based default,
# which shouldn't be used to store highly confidential information
# (create the session table with "rake db:sessions:create")
# ActionController::Base.session_store = :active_record_store
