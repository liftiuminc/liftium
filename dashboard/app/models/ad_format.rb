class AdFormat < ActiveRecord::Base
  validates_presence_of :ad_format_name, :size
  validates_uniqueness_of :ad_format_name
  validates_format_of :size, :with => /^[0-9]{1,3}x[0-9]{1,3}$/

  def name_with_size
    "#{ad_format_name} (#{size})"
  end

end
