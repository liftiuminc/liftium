class PublisherNetworkLoginsController < ApplicationController
  before_filter :require_user

  def index
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
   
    # Get the list of publishers for admin users
    @publishers = Publisher.find :all;
    
    # any conditions to filter by?
    conditions = {}
    %w[publisher_id network_id].each do |id|
        if ! params[id].blank?
            conditions[id] = params[id]
        end
    end        

    @publisher_network_logins = PublisherNetworkLogin.find( 
                                        :all, :conditions => conditions )
  end
  
  def show
    @publisher_network_login = PublisherNetworkLogin.find(params[:id])
  end
  
  def new
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
   
    # Get the list of publishers for admin users
    @publishers = Publisher.find :all;

    @publisher_network_login = PublisherNetworkLogin.new
  end
  
  def create
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
   
    # Get the list of publishers for admin users
    @publishers = Publisher.find :all; 
    
    @publisher_network_login = PublisherNetworkLogin.new(params[:publisher_network_login])
    if @publisher_network_login.save
      flash[:notice] = "Successfully created publisher network login."
      redirect_to @publisher_network_login
    else
      render :action => 'new'
    end
  end
  
  def edit
    # Get a list of enabled networks
    @networks = Network.find :all, :conditions => {:enabled => true}
   
    # Get the list of publishers for admin users
    @publishers = Publisher.find :all;  
  
    @publisher_network_login = PublisherNetworkLogin.find(params[:id])
  end
  
  def update
    @publisher_network_login = PublisherNetworkLogin.find(params[:id])
    if @publisher_network_login.update_attributes(params[:publisher_network_login])
      flash[:notice] = "Successfully updated publisher network login."
      redirect_to publisher_network_logins_url
    else
      # Get a list of enabled networks
      @networks = Network.find :all, :conditions => {:enabled => true}

      # Get the list of publishers for admin users
      @publishers = Publisher.find :all;
    
   
      render :action => 'edit'
    end
  end
  
  def destroy
    @publisher_network_login = PublisherNetworkLogin.find(params[:id])
    @publisher_network_login.destroy
    flash[:notice] = "Successfully destroyed publisher network login."
    redirect_to publisher_network_logins_url
  end
end
