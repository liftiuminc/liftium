require 'test_helper'

class TagTest < ActiveSupport::TestCase
  should_allow_values_for :size, "728x90", "160x600"
  should_not_allow_values_for :size, "adsf", "300x250,300x600"
  should_validate_presence_of :tag_name, :network, :size, :publisher
end
