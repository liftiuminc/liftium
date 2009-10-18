require 'test_helper'

class TagTargetTest < ActiveSupport::TestCase
  should "be valid" do
    assert TagTarget.new.valid?
  end
end
