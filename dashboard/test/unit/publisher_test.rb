require 'test_helper'

class PublisherTest < ActiveSupport::TestCase
  should "be valid" do
    assert Publisher.new.valid?
  end
end
