require 'test_helper'

class TagOptionTest < ActiveSupport::TestCase
  should "be valid" do
    assert TagOption.new.valid?
  end
end
