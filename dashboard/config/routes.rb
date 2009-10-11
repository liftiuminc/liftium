ActionController::Routing::Routes.draw do |map|

  # The priority is based upon order of creation: first created -> highest priority.
  #
  map.resource :account, :controller => "users"
  map.resources :ad_formats
  map.resources :networks, :has_many => :network_tag_options
  map.resources :publishers, :has_many => :tags

  # FIXME: Is there way to not have to list all these?
  map.select_network 'tags/select_network', :controller => 'tags', :action => 'select_network'
  map.tag_generator  'tags/generator/:id', :controller => 'tags', :action => 'generator'
  map.tag_html_preview  'tags/html_preview', :controller => 'tags', :action => 'html_preview'
  map.tag_copy  'tags/copy/:id', :controller => 'tags', :action => 'copy'
  map.resources :tags, :has_many => [ :ad_formats, :tag_options, :tag_targets ]
  map.resource :user_session
  map.resources :users 

  # Charts
  map.chart 'charts/:id/:action', :controller => 'charts'

  # You can have the root of your site routed with map.root -- just remember to delete public/index.html.
  map.root :controller => "welcome"

  # See how all your routes lay out with "rake routes"

  # Install the default routes as the lowest priority.
  # Note: These default routes make all actions in every controller accessible via GET requests. You should
  # consider removing or commenting them out if you're using named routes and resources.
  map.connect ':controller/:action/:id'
  map.connect ':controller/:action/:id.:format'
end
