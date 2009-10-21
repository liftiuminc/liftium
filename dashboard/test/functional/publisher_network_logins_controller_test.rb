require 'test_helper'

class PublisherNetworkLoginsControllerTest < ActionController::TestCase
  setup :activate_authlogic

  context "index action NOT logged in" do
    setup { get :index }
    should_redirect_to "login url" do
      new_user_session_url
    end
  end

  context "index action" do
    should "render index template" do
      login_as_admin
      get :index
      assert_template 'index'
    end
  end
  
  context "show action" do
    should "render show template" do
      login_as_admin
      get :show, :id => PublisherNetworkLogin.first
      assert_template 'show'
    end
  end
  
  context "new action" do
    should "render new template" do
      login_as_admin
      get :new
      assert_template 'new'
    end
  end
  
  context "create action" do
    should "render new template when model is invalid" do
      login_as_admin
      PublisherNetworkLogin.any_instance.stubs(:valid?).returns(false)
      post :create
      assert_template 'new'
    end
    
   # FIXME
   # should "redirect when model is valid" do
   #   PublisherNetworkLogin.any_instance.stubs(:valid?).returns(true)
   #   post :create
   #   assert_redirected_to publisher_network_logins_url(assigns(:publisher_network_logins))
   # end
  end
  
  context "edit action" do
    should "render edit template" do
      login_as_admin
      get :edit, :id => PublisherNetworkLogin.first
      assert_template 'edit'
    end
  end
  
  context "update action" do
    should "render edit template when model is invalid" do
      login_as_admin
      PublisherNetworkLogin.any_instance.stubs(:valid?).returns(false)
      put :update, :id => PublisherNetworkLogin.first
      assert_template 'edit'
    end
  
    should "redirect when model is valid" do
      login_as_admin
      PublisherNetworkLogin.any_instance.stubs(:valid?).returns(true)
      put :update, :id => PublisherNetworkLogin.first
      assert_redirected_to publisher_network_logins_url
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      login_as_admin
      publisher_network_logins = PublisherNetworkLogin.first
      delete :destroy, :id => publisher_network_logins
      assert_redirected_to publisher_network_logins_url
      assert !PublisherNetworkLogin.exists?(publisher_network_logins.id)
    end
  end
end
