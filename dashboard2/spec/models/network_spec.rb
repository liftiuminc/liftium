require File.dirname(__FILE__) + '/../spec_helper'

describe Network do
  it "should be valid" do
    Network.new.should be_valid
  end
end
