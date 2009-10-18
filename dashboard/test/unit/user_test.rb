require 'test_helper'

class UserTest < ActiveSupport::TestCase
  should "be valid" do
    assert User.new.valid?
  end
end
