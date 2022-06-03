FROM nginx

ARG USER_ID
ARG GROUP_ID

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    userdel -f nginx &&\
    if getent group nginx ; then groupdel nginx; fi &&\
    groupadd -g ${GROUP_ID} nginx &&\
    useradd -l -u ${USER_ID} -g nginx nginx &&\
    install -d -m 0755 -o nginx -g nginx /home/nginx\
;fi
