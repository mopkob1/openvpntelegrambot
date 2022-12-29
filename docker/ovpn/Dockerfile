# Original credit: https://github.com/jpetazzo/dockvpn
FROM alpine:latest

LABEL maintainer="Kyle Manna <kyle@kylemanna.com>"

# Testing: pamtester
RUN echo "http://dl-cdn.alpinelinux.org/alpine/edge/testing/" >> /etc/apk/repositories && \
    apk add --update supervisor zip envsubst openvpn iptables bash easy-rsa openvpn-auth-pam google-authenticator pamtester libqrencode && \
    ln -s /usr/share/easy-rsa/easyrsa /usr/local/bin && \
    rm -rf /tmp/* /var/tmp/* /var/cache/apk/* /var/cache/distfiles/*

RUN apk --no-cache add musl-dev git automake autoconf gcc make tzdata
RUN mkdir -p /tmp/netcalc && \
    cd /tmp/netcalc && \
    git clone https://github.com/troglobit/netcalc.git .
RUN cd /tmp/netcalc && ./autogen.sh && ./configure --prefix=/usr --disable-ipcalc-symlink && make -j5 && make install-strip


RUN mkdir -p /opt/pipes

# Needed by scripts
ENV OPENVPN=/etc/openvpn
ENV EASYRSA=/usr/share/easy-rsa \
    EASYRSA_CRL_DAYS=3650 \
    EASYRSA_PKI=$OPENVPN/pki

VOLUME ["/etc/openvpn"]

# Internally uses port 1194/udp, remap using `docker run -p 443:1194/tcp`
EXPOSE 1194/udp

CMD ["ovpn_run"]

ADD ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD ./bin /usr/local/bin
RUN chmod a+x /usr/local/bin/*

# Add support for OTP authentication using a PAM module
ADD ./otp/openvpn /etc/pam.d/
