require 'test_helper'

class DataExportControllerTest < ActionController::TestCase
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

  context "create action" do
    should "render create template" do
      login_as_admin
      get :create, :network_id => "1"
      assert_template 'create'
    end
  end

  context "create action with csv" do
    should "respond with csv data type" do
      login_as_admin
      get :create, :network_id => "1", "format" => "csv"
      respond_with_content_type 'text/csv'
    end
  end

  # Fix bug reported by Jennie where if you do a search that doesn't return results with CSV, it should redirect you back
  context "create action with query that won't return results" do
    setup { 
      login_as_admin
      get :create, :network_id => "100000000000", "format" => "csv"
    }
    should_redirect_to "index" do
      data_export_url
    end
  end
end
