class TagsController < ApplicationController

  # TODO search

  before_filter :require_user
  active_scaffold
  active_scaffold :tag do |config|
    config.columns = [:publisher, :network, :tag_name, :tier, :value, :enabled, :size, :always_fill, :frequency_cap, :rejection_time ]
    config.create.columns = [:publisher, :network, :tag_name, :tier, :value, :enabled, :size, :always_fill, :frequency_cap, :rejection_time, :tag ]
    config.update.columns = [:publisher, :network, :tag_name, :tier, :value, :enabled, :size, :always_fill, :frequency_cap, :rejection_time, :tag ]
    list.sorting = {:tier => 'DESC', :value => 'DESC'}

    config.columns[:always_fill].form_ui = :checkbox
    config.columns[:enabled].form_ui = :checkbox
    config.columns[:network].ui_type = :select
    config.columns[:publisher].ui_type = :select
  end

#   if current_user.admin?
      @tags = Tag.all
#   else 
#      @tags = Tag.find(:all, :conditions => { :publisher_id => current_user.publisher_id })
#   end 

end
