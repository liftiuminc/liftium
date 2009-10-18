require 'test_helper'

class PublisherNetworkLoginsTest < ActiveSupport::TestCase
  should "be valid" do
    assert PublisherNetworkLogins.new.valid?
  end
end
