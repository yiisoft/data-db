FROM gvenzl/oracle-free:23

RUN chown -R 54321:54321 /opt/oracle/oradata && chmod 0777 /opt/oracle/oradata

# COPY ./container-entrypoint-custom.sh ./container-entrypoint-custom.sh
# RUN ["chmod", "+x", "container-entrypoint-custom.sh"]
# RUN ORACLE_PASSWORD=sys_user_password container-entrypoint-custom.sh
