FROM ruby:3.1.2
RUN apt-get update -qq && apt-get install -y --no-install-recommends build-essential libpq-dev cron net-tools netcat && apt-get clean && apt-get autoremove -y && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
