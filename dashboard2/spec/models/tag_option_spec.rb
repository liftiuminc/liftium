require File.dirname(__FILE__) + '/../spec_helper'

describe TagOption do
  fixtures :tags, :tag_options

  it "should be valid" do
    TagOption.new.should be_valid
  end
end
