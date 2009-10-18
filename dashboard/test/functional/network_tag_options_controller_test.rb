require 'test_helper'

class NetworkTagOptionsControllerTest < ActionController::TestCase
  context "index action" do
    should "render index template" do
      get :index
      assert_template 'index'
    end
  end
  
  context "show action" do
    should "render show template" do
      get :show, :id => NetworkTagOption.first
      assert_template 'show'
    end
  end
  
  context "new action" do
    should "render new template" do
      get :new
      assert_template 'new'
    end
  end
  
  context "create action" do
    should "render new template when model is invalid" do
      NetworkTagOption.any_instance.stubs(:valid?).returns(false)
      post :create
      assert_template 'new'
    end
    
    should "redirect when model is valid" do
      NetworkTagOption.any_instance.stubs(:valid?).returns(true)
      post :create
      assert_redirected_to network_tag_option_url(assigns(:network_tag_option))
    end
  end
  
  context "edit action" do
    should "render edit template" do
      get :edit, :id => NetworkTagOption.first
      assert_template 'edit'
    end
  end
  
  context "update action" do
    should "render edit template when model is invalid" do
      NetworkTagOption.any_instance.stubs(:valid?).returns(false)
      put :update, :id => NetworkTagOption.first
      assert_template 'edit'
    end
  
    should "redirect when model is valid" do
      NetworkTagOption.any_instance.stubs(:valid?).returns(true)
      put :update, :id => NetworkTagOption.first
      assert_redirected_to network_tag_option_url(assigns(:network_tag_option))
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      network_tag_option = NetworkTagOption.first
      delete :destroy, :id => network_tag_option
      assert_redirected_to network_tag_options_url
      assert !NetworkTagOption.exists?(network_tag_option.id)
    end
  end
end
