require 'sinatra' 
Sinatra::Application.default_options.merge!( 
                                            :run => false, 
                                            :env => ENV['RACK_ENV'] ) 
require 'graphserver' 
run Sinatra.application
