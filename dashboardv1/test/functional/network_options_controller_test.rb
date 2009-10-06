require 'test_helper'

class NetworkOptionsControllerTest < ActionController::TestCase
  test "should get index" do
    get :index
    assert_response :success
    assert_not_nil assigns(:network_options)
  end

  test "should get new" do
    get :new
    assert_response :success
  end

  test "should create network_option" do
    assert_difference('NetworkOption.count') do
      post :create, :network_option => { }
    end

    assert_redirected_to network_option_path(assigns(:network_option))
  end

  test "should show network_option" do
    get :show, :id => network_options(:one).to_param
    assert_response :success
  end

  test "should get edit" do
    get :edit, :id => network_options(:one).to_param
    assert_response :success
  end

  test "should update network_option" do
    put :update, :id => network_options(:one).to_param, :network_option => { }
    assert_redirected_to network_option_path(assigns(:network_option))
  end

  test "should destroy network_option" do
    assert_difference('NetworkOption.count', -1) do
      delete :destroy, :id => network_options(:one).to_param
    end

    assert_redirected_to network_options_path
  end
end
