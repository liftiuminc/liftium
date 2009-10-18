require 'test_helper'

class TagTest < ActiveSupport::TestCase
  should "be valid" do
    assert Tag.new.valid?
  end
end
