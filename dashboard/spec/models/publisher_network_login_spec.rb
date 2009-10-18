require File.dirname(__FILE__) + '/../spec_helper'

describe PublisherNetworkLogin do
  it "should be valid" do
    PublisherNetworkLogin.new.should be_valid
  end
end
