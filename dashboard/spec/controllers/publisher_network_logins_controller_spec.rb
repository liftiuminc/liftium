require File.dirname(__FILE__) + '/../spec_helper'
 
describe PublisherNetworkLoginsController do
  fixtures :all
  integrate_views
  
  it "index action should render index template" do
    get :index
    response.should render_template(:index)
  end
  
  it "show action should render show template" do
    get :show, :id => PublisherNetworkLogin.first
    response.should render_template(:show)
  end
  
  it "new action should render new template" do
    get :new
    response.should render_template(:new)
  end
  
  it "create action should render new template when model is invalid" do
    PublisherNetworkLogin.any_instance.stubs(:valid?).returns(false)
    post :create
    response.should render_template(:new)
  end
  
  it "create action should redirect when model is valid" do
    PublisherNetworkLogin.any_instance.stubs(:valid?).returns(true)
    post :create
    response.should redirect_to(publisher_network_login_url(assigns[:publisher_network_login]))
  end
  
  it "edit action should render edit template" do
    get :edit, :id => PublisherNetworkLogin.first
    response.should render_template(:edit)
  end
  
  it "update action should render edit template when model is invalid" do
    PublisherNetworkLogin.any_instance.stubs(:valid?).returns(false)
    put :update, :id => PublisherNetworkLogin.first
    response.should render_template(:edit)
  end
  
  it "update action should redirect when model is valid" do
    PublisherNetworkLogin.any_instance.stubs(:valid?).returns(true)
    put :update, :id => PublisherNetworkLogin.first
    response.should redirect_to(publisher_network_login_url(assigns[:publisher_network_login]))
  end
  
  it "destroy action should destroy model and redirect to index action" do
    publisher_network_login = PublisherNetworkLogin.first
    delete :destroy, :id => publisher_network_login
    response.should redirect_to(publisher_network_logins_url)
    PublisherNetworkLogin.exists?(publisher_network_login.id).should be_false
  end
end
