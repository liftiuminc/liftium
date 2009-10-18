require 'test_helper'

class NetworkTagOptionTest < ActiveSupport::TestCase
  should "be valid" do
    assert NetworkTagOption.new.valid?
  end
end
