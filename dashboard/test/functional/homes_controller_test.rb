require 'test_helper'

class HomesControllerTest < ActionController::TestCase
  setup :activate_authlogic

  context "index action NOT logged in" do
    setup { get :index }
    should "render index template" do
      assert_template 'index'
    end
  end

  context "index action logged in as admin" do
    should "render admin template" do
      login_as_admin
      get :admin 
      assert_template 'admin'
    end
  end

  context "publisher action logged in as publisher" do
    should "render publisher template" do
      login_as_publisher
      get :publisher 
      assert_template 'publisher'
    end
  end

  context "admin action NOT logged in" do
    setup { get :admin }
    should_redirect_to "login url" do
      new_user_session_url
    end
  end

  context "publisher action NOT logged in" do
    setup { get :publisher }
    should_redirect_to "login url" do
      new_user_session_url
    end
  end

  context "admin action logged in as PUBLISHER" do
    should "render publisher template" do
      login_as_publisher
      get :admin 
      assert_template "public/403.html"
      respond_with "403"
    end
  end
 
end
