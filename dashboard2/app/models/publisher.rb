class Publisher < ActiveRecord::Base
  attr_accessible :site_name, :site_url, :brand_safety_level, :hoptime
end
