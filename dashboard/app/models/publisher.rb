class Publisher < ActiveRecord::Base
  validates_uniqueness_of :publisher_name
  validates_presence_of :publisher_name
  validates_presence_of :website
  validates_format_of :website, :with => /^https*:\/\/[a-z\-A-Z0-9.]{5,}/, :allow_blank => true
end
