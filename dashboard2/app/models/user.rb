class User < ActiveRecord::Base
  attr_accessible :email, :crypted_password, :password_salt, :persistence_token, :single_access_token, :perishable_token, :login_count, :failed_login_count, :current_login_at, :last_login_at, :current_login_ip, :last_login_ip
  acts_as_authentic
  belongs_to :publisher

  def deliver_password_reset_instructions!
    reset_perishable_token!
    Notifier.deliver_password_reset_instructions(self)
  end

end
