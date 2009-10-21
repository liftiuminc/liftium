require 'test_helper'

class ChartsControllerTest < ActionController::TestCase
  setup :activate_authlogic

  context "index action NOT logged in" do
    setup { get :tag }
    should_redirect_to "login url" do
      new_user_session_url
    end
  end

  context "tag action" do
    should "render tag template" do
      login_as_admin
      get :tag, :id => 13
      assert_template 'tag'
    end
  end

  context "misc_stat action Beacons Served" do
    should "render misc_stat template" do
      login_as_admin
      get :misc_stat, :stat => "BeaconsServed"
      assert_template 'misc_stat'
    end
  end
  
end
