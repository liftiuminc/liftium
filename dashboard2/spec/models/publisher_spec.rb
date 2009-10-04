require File.dirname(__FILE__) + '/../spec_helper'

describe Publisher do
  it "should be valid" do
    Publisher.new.should be_valid
  end
end
