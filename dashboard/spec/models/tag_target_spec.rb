require File.dirname(__FILE__) + '/../spec_helper'

describe TagTarget do
  it "should be valid" do
    TagTarget.new.should be_valid
  end
end
