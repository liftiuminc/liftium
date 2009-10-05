class Publisher < ActiveRecord::Base
  attr_accessible :site_name, :site_url, :brand_safety_level, :hoptime
  has_many :user

  validates_uniqueness_of :site_name
  validates_presence_of :site_name
  validates_presence_of :site_url
  validates_format_of :site_url, :with => /^https*:\/\/[a-z\-A-Z0-9.]{5,}/, :allow_blank => true

end
