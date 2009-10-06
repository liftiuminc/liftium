require File.dirname(__FILE__) + '/../spec_helper'

describe NetworkTagOption do
  it "should be valid" do
    NetworkTagOption.new.should be_valid
  end
end
