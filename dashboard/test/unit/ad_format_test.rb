require 'test_helper'

class AdFormatTest < ActiveSupport::TestCase
  should "be valid" do
    assert AdFormat.new.valid?
  end
end
