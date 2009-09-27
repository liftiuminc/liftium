class TagsController < ApplicationController

  # search
  def conditions_for_collection
    if current_user.admin?
    else 
      ['publisher_id = (?)', current_user.publisher_id]
    end
  end
  
  before_filter :require_user
  active_scaffold
  active_scaffold :tag do |config|
    config.columns = [:publisher, :network, :tag_name, :tier, :value, :enabled, :size, :always_fill, :frequency_cap, :rejection_time ]
    config.create.columns = [:publisher, :network, :tag_name, :tier, :value, :enabled, :always_fill, :size, :frequency_cap, :rejection_time, :tag ]
    config.update.columns = [:publisher, :network, :tag_name, :tier, :value, :enabled, :always_fill, :size, :frequency_cap, :rejection_time, :tag ]
    config.list.sorting = [{:publisher_id => :asc}, {:tier => :desc}, {:value => :desc}]

    config.columns[:always_fill].form_ui = :checkbox
    config.columns[:enabled].form_ui = :checkbox
    config.columns[:network].form_ui = :select
    config.columns[:publisher].form_ui = :select

    config.columns[:tier].description = "0-10";
    config.columns[:frequency_cap].description = "Number, Per 24 hours";
    config.columns[:rejection_time].description = "Wait this many minutes after a rejection before trying again";
    config.columns[:value].label = "Value ($)";

  end
end
