require 'test_helper'

class ChartsControllerTest < ActionController::TestCase
  context "tag action" do
    should "render tag template" do
      get :tag, :id => 13
      assert_template 'tag'
    end
  end

  context "misc_stat action" do
    should "render misc_stat template" do
      get :misc_stat, :stat => "BeaconsServed"
      assert_template 'misc_stat'
    end
  end
  
end
