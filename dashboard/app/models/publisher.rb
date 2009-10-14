class Publisher < ActiveRecord::Base
  has_many :user

  validates_uniqueness_of :site_name
  validates_presence_of :site_name
  validates_presence_of :site_url
  validates_format_of :site_url, :with => /^https*:\/\/[a-z\-A-Z0-9.]{5,}/, :allow_blank => true
  validates_format_of :xdm_iframe_path, :with => /^\/[^ ]+/, :allow_blank => true, :message => "must be a local path Ex. /liftium_iframe.html"
  validates_numericality_of :beacon_throttle, :greater_than_or_equal_to => 0, :less_than_or_equal_to => 1

end
