require 'test_helper'

class AdFormatTest < ActiveSupport::TestCase
  should_allow_values_for :size, "728x90", "160x600"
  should_not_allow_values_for :size, "adsf", "300x250,300x600"
  should_validate_presence_of :ad_format_name, :size
end
