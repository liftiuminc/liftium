# Settings specified here will take precedence over those in config/environment.rb

# In the development environment your application's code is reloaded on
# every request.  This slows down response time but is perfect for development
# since you don't have to restart the webserver when you make code changes.
config.cache_classes = false

# Log error messages when you accidentally call methods on nil.
config.whiny_nils = true

# Show full error reports and disable caching
config.action_controller.consider_all_requests_local = true
config.action_view.debug_rjs                         = true
config.action_controller.perform_caching             = false

# Don't care if the mailer can't send
config.action_mailer.raise_delivery_errors = true


### ExceptionNotification settings. See FB 60
### not yet working, disabled for now --jos
# ExceptionNotifier.configure_exception_notifier do |config|
#     config[:exception_recipients]   = %w(jos@liftium.com)
#     config[:app_name]               = "Liftium Dashboard"
#     config[:sender_address]         = 'dashboard.error@liftium.com'
# end
