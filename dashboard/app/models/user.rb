class User < ActiveRecord::Base
  belongs_to :publisher
  acts_as_authentic

  def admin? 
    publisher_id == nil
  end

  def deliver_password_reset_instructions!
    reset_perishable_token!
    Notifier.deliver_password_reset_instructions(self)
  end

end
