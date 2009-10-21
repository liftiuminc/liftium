require 'test_helper'

class WelcomeControllerTest < ActionController::TestCase
  setup :activate_authlogic

  context "index action NOT logged in" do
    setup { get :index }
    should "render hello template" do
      new_user_session_url
      assert_template 'hello'
    end
  end

  context "index action" do
    should "render index template" do
      login_as_publisher
      get :index
      assert_template 'index'
    end
  end
end
