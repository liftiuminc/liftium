class AdFormat < ActiveRecord::Base
  attr_accessible :ad_format_name, :size
  belongs_to :tag
end
