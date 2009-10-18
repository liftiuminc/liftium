require 'test_helper'

class UserSessionsControllerTest < ActionController::TestCase
  context "new action" do
    should "render new template" do
      get :new
      assert_template 'new'
    end
  end
  
  context "create action" do
    should "render new template when model is invalid" do
      UserSession.any_instance.stubs(:valid?).returns(false)
      post :create
      assert_template 'new'
    end
    
    should "redirect when model is valid" do
      UserSession.any_instance.stubs(:valid?).returns(true)
      post :create
      assert_redirected_to root_url
    end
  end
  
  context "destroy action" do
    should "destroy model and redirect to index action" do
      user_session = UserSession.first
      delete :destroy, :id => user_session
      assert_redirected_to root_url
      assert !UserSession.exists?(user_session.id)
    end
  end
end
