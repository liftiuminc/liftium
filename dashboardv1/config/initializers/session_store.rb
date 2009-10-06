# Be sure to restart your server when you modify this file.

# Your secret key for verifying cookie session data integrity.
# If you change this key, all old sessions will become invalid!
# Make sure the secret is at least 30 characters and all random, 
# no regular words or you'll be exposed to dictionary attacks.
ActionController::Base.session = {
  :key         => '_dashboard_session',
  :secret      => '8c4a795fe5f458ce1c3f6a2f442ca13dff83eacdf43463babcc910a8b3fc011b2b16c4d65dee87b96b054a8768aac47abdf862ce4e9f0da6e4774e59c10c246b'
}

# Use the database for sessions instead of the cookie-based default,
# which shouldn't be used to store highly confidential information
# (create the session table with "rake db:sessions:create")
# ActionController::Base.session_store = :active_record_store
