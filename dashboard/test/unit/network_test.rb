require 'test_helper'

class NetworkTest < ActiveSupport::TestCase
  should "be valid" do
    assert Network.new.valid?
  end
end
