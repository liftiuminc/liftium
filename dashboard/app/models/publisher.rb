class Publisher < ActiveRecord::Base
  require 'uri'

  has_many :user

  validates_uniqueness_of :site_name
  validates_presence_of :site_name
  validates_presence_of :site_url
  validates_format_of :xdm_iframe_path, :with => /^\/[^ ]+/, :allow_blank => true, :message => "must be a local path Ex. /liftium_iframe.html"
  validates_numericality_of :beacon_throttle, :greater_than_or_equal_to => 0, :less_than_or_equal_to => 1

   ### make sure all urls start with http(s?). See FB 32
   def site_url=(url)

      ### this will catch any malformed uris
      if url.length > 0
         url = URI.parse( url =~ /^https?/i ? url : 'http://' + url ).to_s
      end  
      
      write_attribute( :site_url, url )
   end

end
